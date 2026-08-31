param(
  [string]$Root = "D:\Mood_Foam\public_html",
  [int]$MaxDim = 2000,
  [int]$JpegQuality = 80
)

Add-Type -AssemblyName System.Drawing

function Get-RotatedImage($img) {
  if ($img.PropertyIdList -contains 0x0112) {
    $orientation = $img.GetPropertyItem(0x0112).Value[0]
    switch ($orientation) {
      2 { $img.RotateFlip([System.Drawing.RotateFlipType]::RotateNoneFlipX) }
      3 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate180FlipNone) }
      4 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate180FlipX) }
      5 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate90FlipX) }
      6 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate90FlipNone) }
      7 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate270FlipX) }
      8 { $img.RotateFlip([System.Drawing.RotateFlipType]::Rotate270FlipNone) }
    }
    $img.RemovePropertyItem(0x0112)
  }
  return $img
}

$jpegCodec = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' }
$encParams = New-Object System.Drawing.Imaging.EncoderParameters(1)
$encParams.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter([System.Drawing.Imaging.Encoder]::Quality, [int64]$JpegQuality)

$files = Get-ChildItem -Path $Root -Recurse -Include *.jpg,*.jpeg,*.JPG,*.JPEG,*.png,*.PNG -File | Where-Object {
  $_.FullName -notmatch [regex]::Escape("favicon_io") -and $_.FullName -notmatch [regex]::Escape("images\logo.png")
}

$before = 0
$after = 0
$count = 0
$errors = @()

foreach ($f in $files) {
  try {
    $originalSize = $f.Length
    $before += $originalSize

    $bytes = [System.IO.File]::ReadAllBytes($f.FullName)
    $ms = New-Object System.IO.MemoryStream(,$bytes)
    $img = [System.Drawing.Image]::FromStream($ms)
    $img = Get-RotatedImage $img

    $w = $img.Width
    $h = $img.Height
    $scale = 1.0
    if ($w -gt $MaxDim -or $h -gt $MaxDim) {
      $scale = [math]::Min($MaxDim / $w, $MaxDim / $h)
    }
    $newW = [int]([math]::Round($w * $scale))
    $newH = [int]([math]::Round($h * $scale))

    $bmp = New-Object System.Drawing.Bitmap($newW, $newH)
    $bmp.SetResolution(96,96)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    $g.PixelOffsetMode = [System.Drawing.Drawing2D.PixelOffsetMode]::HighQuality
    $g.DrawImage($img, 0, 0, $newW, $newH)
    $g.Dispose()
    $img.Dispose()
    $ms.Dispose()

    $ext = $f.Extension.ToLower()
    $tempPath = "$($f.FullName).tmp"
    if ($ext -eq ".jpg" -or $ext -eq ".jpeg") {
      $bmp.Save($tempPath, $jpegCodec, $encParams)
    } else {
      $bmp.Save($tempPath, [System.Drawing.Imaging.ImageFormat]::Png)
    }
    $bmp.Dispose()

    $newSize = (Get-Item $tempPath).Length
    if ($newSize -lt $originalSize) {
      Move-Item -Path $tempPath -Destination $f.FullName -Force
      $after += $newSize
    } else {
      Remove-Item $tempPath -Force
      $after += $originalSize
    }
    $count++
  } catch {
    $errors += "$($f.FullName): $_"
    $after += $f.Length
  }
}

"Processed: $count files"
"Before: {0:N1} MB" -f ($before / 1MB)
"After:  {0:N1} MB" -f ($after / 1MB)
"Saved:  {0:N1} MB ({1:N0}%)" -f (($before-$after) / 1MB), ((($before-$after)/$before)*100)
if ($errors.Count -gt 0) {
  "Errors:"
  $errors
}

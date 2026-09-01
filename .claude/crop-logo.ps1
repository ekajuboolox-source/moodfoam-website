Add-Type -AssemblyName System.Drawing

$src = "D:\Mood_Foam\public_html\images\logo.png"
$out = "D:\Mood_Foam\public_html\images\logo.png"
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$w = $bmp.Width
$h = $bmp.Height
"Source: ${w}x${h}"

$minX = $w; $maxX = -1; $minY = $h; $maxY = -1
for ($y = 0; $y -lt $h; $y++) {
  for ($x = 0; $x -lt $w; $x++) {
    $p = $bmp.GetPixel($x, $y)
    $isContent = ($p.A -gt 15) -and -not ($p.R -gt 245 -and $p.G -gt 245 -and $p.B -gt 245)
    if ($isContent) {
      if ($x -lt $minX) { $minX = $x }
      if ($x -gt $maxX) { $maxX = $x }
      if ($y -lt $minY) { $minY = $y }
      if ($y -gt $maxY) { $maxY = $y }
    }
  }
}
"Content bbox: x=$minX-$maxX, y=$minY-$maxY (out of ${w}x${h})"

$pad = 8
$minX = [Math]::Max(0, $minX - $pad)
$minY = [Math]::Max(0, $minY - $pad)
$maxX = [Math]::Min($w - 1, $maxX + $pad)
$maxY = [Math]::Min($h - 1, $maxY + $pad)
$cropW = $maxX - $minX
$cropH = $maxY - $minY
"Cropping to: x=$minX y=$minY w=$cropW h=$cropH"

$cropped = New-Object System.Drawing.Bitmap($cropW, $cropH)
$g = [System.Drawing.Graphics]::FromImage($cropped)
$g.DrawImage($bmp, (New-Object System.Drawing.Rectangle(0,0,$cropW,$cropH)), (New-Object System.Drawing.Rectangle($minX,$minY,$cropW,$cropH)), [System.Drawing.GraphicsUnit]::Pixel)
$g.Dispose()
$bmp.Dispose()

$cropped.Save("D:\Mood_Foam\.claude\logo-cropped-preview.png", [System.Drawing.Imaging.ImageFormat]::Png)
$cropped.Dispose()
"Saved preview to D:\Mood_Foam\.claude\logo-cropped-preview.png (${cropW}x${cropH})"

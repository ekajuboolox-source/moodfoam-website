Add-Type -AssemblyName System.Drawing

$root = "D:\Mood_Foam\public_html"
$src = [System.Drawing.Bitmap]::FromFile("$root\images\logo.png")

# Auto-detect the bounding box of the bed glyph: scan the left 45% of the
# source (where the icon mark sits, left of the wordmark text) for pixels
# that are both opaque and not near-white, i.e. the gold/black glyph ink.
$scanW = [int]($src.Width * 0.37)
$minX = $src.Width; $maxX = 0; $minY = $src.Height; $maxY = 0
for ($y = 0; $y -lt $src.Height; $y++) {
  for ($x = 0; $x -lt $scanW; $x++) {
    $p = $src.GetPixel($x, $y)
    if ($p.A -gt 20 -and -not ($p.R -gt 235 -and $p.G -gt 235 -and $p.B -gt 235)) {
      if ($x -lt $minX) { $minX = $x }
      if ($x -gt $maxX) { $maxX = $x }
      if ($y -lt $minY) { $minY = $y }
      if ($y -gt $maxY) { $maxY = $y }
    }
  }
}
$pad = 6
$minX = [math]::Max(0, $minX - $pad)
$minY = [math]::Max(0, $minY - $pad)
$maxX = [math]::Min($src.Width - 1, $maxX + $pad)
$maxY = [math]::Min($src.Height - 1, $maxY + $pad)
$cropW = $maxX - $minX
$cropH = $maxY - $minY

"Detected glyph bounds: x=$minX y=$minY w=$cropW h=$cropH"

$glyph = New-Object System.Drawing.Bitmap($cropW, $cropH)
$gg = [System.Drawing.Graphics]::FromImage($glyph)
$gg.DrawImage($src, (New-Object System.Drawing.Rectangle(0,0,$cropW,$cropH)), (New-Object System.Drawing.Rectangle($minX,$minY,$cropW,$cropH)), [System.Drawing.GraphicsUnit]::Pixel)
$gg.Dispose()

function New-TouchIcon($size, $outPath) {
  $canvas = New-Object System.Drawing.Bitmap($size, $size)
  $g = [System.Drawing.Graphics]::FromImage($canvas)
  $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
  $g.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
  $bg = [System.Drawing.ColorTranslator]::FromHtml("#14331f")
  $g.Clear($bg)

  # Keep the glyph inside the ~66% safe-zone circle Android uses for
  # adaptive/round icon masking, so nothing gets clipped by a circular crop.
  $targetW = $size * 0.62
  $scale = $targetW / $cropW
  $drawW = $cropW * $scale
  $drawH = $cropH * $scale
  $offX = ($size - $drawW) / 2
  $offY = ($size - $drawH) / 2
  $g.DrawImage($glyph, $offX, $offY, $drawW, $drawH)
  $g.Dispose()

  $canvas.Save($outPath, [System.Drawing.Imaging.ImageFormat]::Png)
  $canvas.Dispose()
  "Saved $outPath ($size x $size)"
}

New-TouchIcon 180 "$root\favicon_io\apple-touch-icon.png"
New-TouchIcon 192 "$root\favicon_io\android-chrome-192x192.png"
New-TouchIcon 512 "$root\favicon_io\android-chrome-512x512.png"

$glyph.Dispose()
$src.Dispose()

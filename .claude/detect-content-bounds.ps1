param(
  [string[]]$Files
)
Add-Type -AssemblyName System.Drawing

foreach ($f in $Files) {
  $bmp = [System.Drawing.Bitmap]::FromFile($f)
  $w = $bmp.Width
  $h = $bmp.Height
  $threshold = 235 # pixels lighter than this on all channels count as "white background"

  $topContent = -1
  $bottomContent = -1

  for ($y = 0; $y -lt $h; $y++) {
    $nonWhiteCount = 0
    for ($x = 0; $x -lt $w; $x += [Math]::Max(1, [int]($w / 60))) {
      $p = $bmp.GetPixel($x, $y)
      if ($p.R -lt $threshold -or $p.G -lt $threshold -or $p.B -lt $threshold) {
        $nonWhiteCount++
      }
    }
    if ($nonWhiteCount -ge 2) {
      if ($topContent -eq -1) { $topContent = $y }
      $bottomContent = $y
    }
  }

  $topPct = [math]::Round(($topContent / $h) * 100, 1)
  $bottomPct = [math]::Round(($bottomContent / $h) * 100, 1)
  $centerPct = [math]::Round((($topContent + $bottomContent) / 2 / $h) * 100, 1)

  "$f : ${w}x${h}, content from y=$topContent ($topPct%) to y=$bottomContent ($bottomPct%), content-center at $centerPct%"
  $bmp.Dispose()
}

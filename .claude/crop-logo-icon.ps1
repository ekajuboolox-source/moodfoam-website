Add-Type -AssemblyName System.Drawing

$src = "D:\Mood_Foam\public_html\images\logo.png"
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$w = $bmp.Width
$h = $bmp.Height

function IsBackground($px) {
    # transparent, or near-white
    if ($px.A -lt 10) { return $true }
    if ($px.R -gt 245 -and $px.G -gt 245 -and $px.B -gt 245) { return $true }
    return $false
}

# For each column, does it contain any non-background pixel?
$colHasContent = New-Object bool[] $w
for ($x = 0; $x -lt $w; $x++) {
    $found = $false
    for ($y = 0; $y -lt $h; $y++) {
        $px = $bmp.GetPixel($x, $y)
        if (-not (IsBackground $px)) { $found = $true; break }
    }
    $colHasContent[$x] = $found
}

# Find first content column
$firstContent = -1
for ($x = 0; $x -lt $w; $x++) { if ($colHasContent[$x]) { $firstContent = $x; break } }

# Find gaps (runs of columns with no content) after the first content column
$gaps = @()
$x = $firstContent
$inGap = $false
$gapStart = -1
while ($x -lt $w) {
    if (-not $colHasContent[$x]) {
        if (-not $inGap) { $inGap = $true; $gapStart = $x }
    } else {
        if ($inGap) {
            $gaps += [PSCustomObject]@{ Start = $gapStart; End = ($x - 1); Width = ($x - $gapStart) }
            $inGap = $false
        }
    }
    $x++
}

Write-Host "First content column: $firstContent"
Write-Host "Gaps found (start,end,width):"
$gaps | ForEach-Object { Write-Host " $($_.Start) - $($_.End)  (width $($_.Width))" }

$bmp.Dispose()

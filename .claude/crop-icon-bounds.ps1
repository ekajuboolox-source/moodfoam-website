Add-Type -AssemblyName System.Drawing

$src = "D:\Mood_Foam\public_html\images\logo.png"
$bmp = [System.Drawing.Bitmap]::FromFile($src)
$h = $bmp.Height

$xStart = 47
$xEnd = 218

function IsBackground($px) {
    if ($px.A -lt 10) { return $true }
    if ($px.R -gt 245 -and $px.G -gt 245 -and $px.B -gt 245) { return $true }
    return $false
}

$minY = -1
$maxY = -1
for ($y = 0; $y -lt $h; $y++) {
    $found = $false
    for ($x = $xStart; $x -le $xEnd; $x++) {
        $px = $bmp.GetPixel($x, $y)
        if (-not (IsBackground $px)) { $found = $true; break }
    }
    if ($found) {
        if ($minY -eq -1) { $minY = $y }
        $maxY = $y
    }
}

Write-Host "Icon column range: $xStart - $xEnd"
Write-Host "Icon row range: $minY - $maxY"
$bmp.Dispose()

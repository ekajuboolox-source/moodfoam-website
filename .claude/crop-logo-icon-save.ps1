Add-Type -AssemblyName System.Drawing

$src = "D:\Mood_Foam\public_html\images\logo.png"
$dst = "D:\Mood_Foam\public_html\images\logo-icon.png"

$bmp = [System.Drawing.Bitmap]::FromFile($src)

$pad = 4
$x = 47 - $pad
$y = 203 - $pad
$w = (218 - 47) + 1 + ($pad * 2)
$h = (328 - 203) + 1 + ($pad * 2)

$rect = New-Object System.Drawing.Rectangle($x, $y, $w, $h)
$cropped = New-Object System.Drawing.Bitmap($w, $h, [System.Drawing.Imaging.PixelFormat]::Format32bppArgb)
$g = [System.Drawing.Graphics]::FromImage($cropped)
$g.DrawImage($bmp, (New-Object System.Drawing.Rectangle(0,0,$w,$h)), $rect, [System.Drawing.GraphicsUnit]::Pixel)
$g.Dispose()

$cropped.Save($dst, [System.Drawing.Imaging.ImageFormat]::Png)
Write-Host "Saved $dst  (${w}x${h})"

$cropped.Dispose()
$bmp.Dispose()

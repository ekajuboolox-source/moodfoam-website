# Generates public_html/images/og-image.jpg (1200x630), the image shown when a
# page is shared on WhatsApp / Facebook / X / LinkedIn (og:image + twitter:image).
# Run from anywhere:  powershell -File .claude\make-og-image.ps1
param([string]$Root = (Join-Path $PSScriptRoot '..\public_html'))

Add-Type -AssemblyName System.Drawing
$W = 1200; $H = 630
$bmp = New-Object System.Drawing.Bitmap $W, $H
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = 'AntiAlias'; $g.TextRenderingHint = 'AntiAliasGridFit'
$g.InterpolationMode = 'HighQualityBicubic'

# Brand-green background gradient (same family as the footer/header)
$rect = New-Object System.Drawing.Rectangle 0, 0, $W, $H
$c1 = [System.Drawing.ColorTranslator]::FromHtml('#14331f')
$c2 = [System.Drawing.ColorTranslator]::FromHtml('#0a8010')
$bg = New-Object System.Drawing.Drawing2D.LinearGradientBrush $rect, $c1, $c2, 35
$g.FillRectangle($bg, $rect)

# Soft light band for depth
$band = New-Object System.Drawing.SolidBrush ([System.Drawing.Color]::FromArgb(18, 255, 255, 255))
$g.FillEllipse($band, 620, -260, 900, 900)

$white = [System.Drawing.Brushes]::White
$gold  = New-Object System.Drawing.SolidBrush ([System.Drawing.ColorTranslator]::FromHtml('#d4a437'))
$mint  = New-Object System.Drawing.SolidBrush ([System.Drawing.ColorTranslator]::FromHtml('#eafff1'))

# Logo icon (gold bed mark)
$icon = [System.Drawing.Image]::FromFile((Join-Path $Root 'images\logo-icon.png'))
$ih = 170; $iw = [int]($icon.Width * $ih / $icon.Height)
$g.DrawImage($icon, 80, 120, $iw, $ih)
$icon.Dispose()

$fBig  = New-Object System.Drawing.Font 'Segoe UI', 64, ([System.Drawing.FontStyle]::Bold), 'Pixel'
$fSub  = New-Object System.Drawing.Font 'Segoe UI', 40, ([System.Drawing.FontStyle]::Regular), 'Pixel'
$fTag  = New-Object System.Drawing.Font 'Segoe UI', 30, ([System.Drawing.FontStyle]::Regular), 'Pixel'
$fSm   = New-Object System.Drawing.Font 'Segoe UI', 26, ([System.Drawing.FontStyle]::Bold), 'Pixel'

$x = 80 + $iw + 36
$g.DrawString('MOODFOAM', $fBig, $white, $x, 108)
$g.DrawString('MATTRESSES', $fBig, $white, $x, 182)

$g.DrawString('Better Nights, Better Days', $fSub, $gold, 80, 340)
$g.DrawString('Foam & spring mattresses  |  Beds  |  Sofas  |  Beddings', $fTag, $mint, 80, 415)

# Footer line
$pen = New-Object System.Drawing.Pen ([System.Drawing.Color]::FromArgb(70, 255, 255, 255)), 2
$g.DrawLine($pen, 80, 510, 1120, 510)
$g.DrawString('Made in Mityana, Uganda', $fSm, $white, 80, 535)
$g.DrawString('www.moodfoammattresses.com', $fSm, $gold, 700, 535)

# Save as JPEG, quality 85
$codec = [System.Drawing.Imaging.ImageCodecInfo]::GetImageEncoders() | Where-Object { $_.MimeType -eq 'image/jpeg' }
$ep = New-Object System.Drawing.Imaging.EncoderParameters 1
$ep.Param[0] = New-Object System.Drawing.Imaging.EncoderParameter ([System.Drawing.Imaging.Encoder]::Quality), 85L
$out = Join-Path $Root 'images\og-image.jpg'
$bmp.Save($out, $codec, $ep)
$g.Dispose(); $bmp.Dispose()
"Wrote $out ($((Get-Item $out).Length) bytes)"

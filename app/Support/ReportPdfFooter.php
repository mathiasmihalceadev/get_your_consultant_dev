<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

class ReportPdfFooter
{
    public static function render(?DateTimeInterface $generatedAt = null): string
    {
        $timestamp = $generatedAt
            ? Carbon::instance(\DateTime::createFromInterface($generatedAt))
            : now();

        $generatedDate = $timestamp->format('d.m.Y');
        $logo = self::logoDataUri();
        $checkIcon = self::iconSvg(
            'M173.66,98.34a8,8,0,0,1,0,11.32l-56,56a8,8,0,0,1-11.32,0l-24-24a8,8,0,0,1,11.32-11.32L112,148.69l50.34-50.35A8,8,0,0,1,173.66,98.34ZM232,128A104,104,0,1,1,128,24,104.11,104.11,0,0,1,232,128Zm-16,0a88,88,0,1,0-88,88A88.1,88.1,0,0,0,216,128Z',
            '#16a34a',
            20
        );
        return <<<HTML
<div style="width:100%;padding:0 16px;font-family:Inter,sans-serif;box-sizing:border-box;">
    <div style="display:flex;align-items:center;gap:14px;background:#ffffff;border:1px solid #dfe3f3;border-radius:8px;padding:6px 10px;">
        <div style="flex:1 1 0;min-width:0;display:flex;align-items:flex-start;gap:8px;">
            <div style="width:30px;flex:0 0 30px;font-size:0;line-height:1;">{$checkIcon}</div>
            <div style="min-width:0;">
                <div style="font-size:10px;line-height:1.22;font-weight:700;color:#1f2a44;margin-bottom:1px;white-space:nowrap;">Raport de proprietate verificat</div>
                <div style="font-size:8.5px;line-height:1.45;color:#334155;">
                    <span style="display:block;white-space:nowrap;">Acest raport este generat pe baza datelor publice disponibile</span>
                    <span style="display:block;white-space:nowrap;">din piață și a analizelor comparative statistice.</span>
                </div>
            </div>
        </div>
        <div style="flex:0 0 auto;display:flex;align-items:center;justify-content:center;padding:0 6px;">
            <img src="{$logo}" alt="GetYourConsultant" style="display:block;width:150px;max-width:150px;max-height:30px;height:auto;object-fit:contain;margin:0 auto;" />
        </div>
        <div style="flex:1 1 0;min-width:0;display:flex;justify-content:flex-end;">
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;white-space:nowrap;">
                <div style="font-size:10px;line-height:1.25;font-weight:500;color:#334155;">Generat la: <span style="font-weight:700;color:#1f2a44;">{$generatedDate}</span></div>
                <div style="font-size:10px;line-height:1.25;font-weight:500;color:#334155;">Valabilitate analiză: <span style="font-weight:700;color:#1f2a44;">30 zile</span></div>
            </div>
        </div>
    </div>
    <div style="margin-top:10px;text-align:center;font-size:8px;line-height:1.2;font-weight:500;color:#aeb7c5;">GetYourConsultant - Toate drepturile rezervate.</div>
</div>
HTML;
    }

    private static function logoDataUri(): string
    {
        $path = public_path('images/logo-white.jpg');

        if (!is_file($path)) {
            return '';
        }

        $binary = file_get_contents($path);

        if ($binary === false) {
            return '';
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return 'data:' . $mimeType . ';base64,' . base64_encode($binary);
    }

    private static function iconSvg(string $path, string $fillColor, int $size = 16): string
    {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 256 256" fill="%s" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="%s"/></svg>',
            $size,
            $size,
            $fillColor,
            $path
        );
    }
}

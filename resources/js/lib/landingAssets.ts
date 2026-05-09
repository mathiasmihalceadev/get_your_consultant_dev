export type LandingAssetVersion = "v1" | "v2";

// Change this value to switch the landing/wizard image set.
export const landingAssetVersion: LandingAssetVersion = "v2";

const versionedImagesBasePath =
    landingAssetVersion === "v2" ? "/images/v2" : "/images";

const baseImagesPath = "/images";

const getLandingAssetPath = (
    filename: string,
    options?: { useVersionedPath?: boolean },
) => {
    const folder =
        options?.useVersionedPath === false
            ? baseImagesPath
            : versionedImagesBasePath;

    return `${folder}/${filename}`;
};

export const landingAssets = {
    heroImageSrc: getLandingAssetPath("hero-property-analysis.png"),
    reportImageSrc: getLandingAssetPath("report-first-page-illustration.png"),
    ctaImageSrc: getLandingAssetPath("cta-apartment-cutout.png"),
    pricingBuyingImageSrc: getLandingAssetPath("pricing-buying-apartment.png"),
    pricingRentalImageSrc: getLandingAssetPath("pricing-rental-apartmen.png"),
    // v2 currently doesn't include the paper texture, so this stays on the base path.
    textureImageSrc: getLandingAssetPath("blue-noise-texture.png", {
        useVersionedPath: false,
    }),
} as const;

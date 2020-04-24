/**
 * Load Nucleo if:
 *  - is not WP
 *  - is WP and Pro
 */
export const loadNucleo = (isPro: boolean, isWP: boolean): boolean =>
  !(isWP && !isPro);

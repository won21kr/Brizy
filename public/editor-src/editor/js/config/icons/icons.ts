import { Icon, read } from "visual/config/icons/Icon";
import outline from "./iconTypes/outline.json";
import glyph from "./iconTypes/glyph.json";
import fa from "./iconTypes/fa.json";

const readIcons = (arr: unknown[]): Icon[] =>
  arr.map(read).filter(Boolean) as Icon[];

export const icons = readIcons([...outline, ...glyph, ...fa]);

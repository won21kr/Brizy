import { loadNucleo } from "visual/component/Prompts/PromptIcon/utils";

describe("Testing 'loadNucleo' function", function() {
  test("Return true if is not WP", () => {
    expect(loadNucleo(false, false)).toBe(true);
    expect(loadNucleo(true, false)).toBe(true);
  });

  test("Return true if is WP and is Pro", () => {
    expect(loadNucleo(true, true)).toBe(true);
  });

  test("Return false if is WP and is not Pro", () => {
    expect(loadNucleo(false, true)).toBe(false);
  });
});

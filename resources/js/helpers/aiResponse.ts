import {AiVendorType} from "../types/aiVendorType";
import markdownit from "markdown-it";
import dot from "dot-object";

const md = markdownit()
export const prettifyAiResponse = (output, aiVendor) => {
    if (aiVendor === AiVendorType.OPENAI) {
        return md.render(dot.pick('choices.0.message.content', output));
    }

    if (aiVendor === AiVendorType.ANTHROPIC) {
        return dot.pick('', output)
    }

    return output
}

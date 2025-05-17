import {AiVendorType} from "./aiVendorType";
import {VariableType} from "./variableType";

const propsMapping = {
    "max_tokens": VariableType.INT,
    "temperature": VariableType.FLOAT,
    "top_p": VariableType.FLOAT,
    "top_k": VariableType.FLOAT
}

export const AiVendorOptionsTemplates = {
    [AiVendorType.OPENAI]: [
        "max_tokens",
        "model",
        "temperature",
        "top_p",
    ],
    [AiVendorType.ANTHROPIC]: [
        "max_tokens",
        "model",
        "temperature",
        "top_p",
        "top_k",
    ]
}
export const toValuesModalVariables = (values) => {
    return Object.keys(values).map((key) => {
        return {
            name: key, type: propsMapping[key as keyof typeof propsMapping] ?? null
        }
    })
}

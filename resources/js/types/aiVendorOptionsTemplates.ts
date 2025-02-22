import {AiVendorType} from "./aiVendorType";
import {VariableType} from "./variableType";


//     'n',
//     'max_tokens',
//     'model',
//     'frequency_penalty',
//     'logit_bias',
//     'presence_penalty',
//     'response_format',
//     'seed',
//     'stop',
//     'temperature',
//     'top_p',



// 'max_tokens',
//     'model',
//     'top_k',
//     'temperature',
//     'top_p',
//     'stop_sequences',

export const AiVendorOptionsTemplates = {
    [AiVendorType.OPENAI]: [
        {name: "max_tokens", type: VariableType.INT},
        {name: "model"},
        {name: "temperature", type: VariableType.FLOAT},
        {name: "top_p", type: VariableType.FLOAT},
    ],
    [AiVendorType.ANTHROPIC]: [
        {name: "max_tokens", type: VariableType.INT},
        {name: "model"},
        {name: "temperature", type: VariableType.FLOAT},
        {name: "top_p", type: VariableType.FLOAT},
        {name: "top_k", type: VariableType.FLOAT},
    ]
}

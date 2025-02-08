import {AiVendorType} from "./aiVendorType";


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
        {name: "max_tokens"},
        {name: "model"},
        {name: "temperature"},
        {name: "top_p"},
    ],
    [AiVendorType.ANTHROPIC]: [
        {name: "max_tokens"},
        {name: "model"},
        {name: "temperature"},
        {name: "top_p"},
        {name: "top_k"},
    ]
}

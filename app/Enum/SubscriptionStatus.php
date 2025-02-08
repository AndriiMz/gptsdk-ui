<?php

namespace App\Enum;

enum SubscriptionStatus: string
{
    case PAID = 'paid';
    case FREE = 'free';
    case PAST_DUE = 'past_due';

}

<?php

namespace Dentsu\MAPP;

class MAPPUser
{
    public function __construct(
        public int $id,
        public string $email,
        public ?string $mobileNumber,
        public ?string $identifier,
        public ?string $unifiedIdentifiers,
        public ?string $channelAddresses
    ) {
        //
    }
}

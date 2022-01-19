<?php

namespace Dentsu\MAPP;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MAPP
{
    public function __construct(
        private string $endpoint,
        private string $username,
        private string $password
    ) {
        //
    }

    public function firstOrCreate(string $email, array $payload = []): MAPPUser
    {
//            $payload['user.identifier'] = $userId;
        $mappUser = $this->getByEmail($email);
        if ($mappUser === null) {
            $mappUser = $this->userCreate($email, $payload);
        }

        return $mappUser;
    }

    public function getByEmail(string $email): ?MAPPUser
    {
        $res = $this->getClient()
            ->get(
                $this->endpoint . '/user/getByEmail',
                [
                    'email' => $email,
                ]
            );

        Log::info(json_encode($res->json()));

        if (isset($res->json()['errorActor'])) {
            return null;
        }

        return new MAPPUser(...$res->json());
    }

    public function userCreate(string $email, array $payload): MAPPUser
    {
        $res = $this->getClient()
            ->post(
                $this->endpoint . '/user/create?email=' . $email,
                $this->buildPayload($payload)
            );

        Log::info(json_encode($res->json()));

        if (isset($res->json()['errorActor'])) {
            Log::info(__METHOD__);
            Log::info('error on ' . $this->endpoint . '/user/create?email=' . $email);
            
            throw new InvalidArgumentException($res->json()['message']);
        }

        return new MAPPUser(...$res->json());
    }

    public function sendEmail(string $email, int $templateId, array $parameters)
    {
        Log::info(__METHOD__ . ' _ START');

        $mappUser = $this->firstOrCreate($email);
        
        Log::info(json_encode($mappUser));
        
        $data = [
            'recipientId' => $mappUser->id,
            'messageId' => $templateId,
        ];
        
        $res = $this->getClient()
            ->post(
                $this->endpoint . '/message/sendSingle?' . http_build_query($data),
                ['parameters' => $this->buildPayload($parameters)]
            );
        
        Log::info(json_encode($res->json()));
        Log::info(__METHOD__ . ' _ END');
    }

    private function buildPayload(array $payload)
    {
        $mappPayload = [];
        foreach ($payload as $key => $value) {
            $mappPayload[] = ['name' => $key, 'value' => $value];
        }

        return $mappPayload;
    }

    private function getClient(): PendingRequest
    {
        return Http::withBasicAuth(
            $this->username,
            $this->password
        )
            ->acceptJson();
    }
}

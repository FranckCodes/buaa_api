<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OtpService
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    public function generateLoginOtp(string $telephone): OtpCode
    {
        $user = User::with('status')->where('telephone', $telephone)->first();

        if (!$user) {
            throw new RuntimeException('Aucun utilisateur trouvé avec ce numéro.');
        }

        if ($user->status?->code !== 'actif') {
            throw new RuntimeException('Ce compte n\'est pas actif.');
        }

        return DB::transaction(function () use ($user, $telephone) {
            // Supprimer les anciens OTP non utilisés pour ce téléphone
            OtpCode::where('telephone', $telephone)
                ->where('purpose', 'login')
                ->where('is_used', false)
                ->delete();

            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $otp = OtpCode::create([
                'user_id'    => $user->id,
                'telephone'  => $telephone,
                'code'       => $code,
                'purpose'    => 'login',
                'expires_at' => now()->addMinutes(5),
                'attempts'   => 0,
                'is_used'    => false,
            ]);

            $this->smsService->sendOtp($telephone, $code);

            return $otp;
        });
    }

    public function verifyLoginOtp(string $telephone, string $code): User
    {
        $otp = OtpCode::where('telephone', $telephone)
            ->where('purpose', 'login')
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp) {
            throw new RuntimeException('Aucun OTP valide trouvé.');
        }

        if (now()->greaterThan($otp->expires_at)) {
            throw new RuntimeException('Le code OTP a expiré.');
        }

        if ($otp->attempts >= 5) {
            throw new RuntimeException('Nombre maximum de tentatives atteint.');
        }

        if ($otp->code !== $code) {
            $otp->increment('attempts');
            throw new RuntimeException('Code OTP invalide.');
        }

        $otp->update([
            'is_used'     => true,
            'verified_at' => now(),
        ]);

        return $otp->user()->with(['roles', 'status', 'clientProfile'])->firstOrFail();
    }
}

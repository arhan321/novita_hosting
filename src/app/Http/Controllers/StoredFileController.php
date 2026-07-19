<?php

namespace App\Http\Controllers;

use App\Models\OrderFile;
use App\Models\Payment;
use App\Support\StoragePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StoredFileController extends Controller
{
    public function orderFile(Request $request, OrderFile $orderFile): StreamedResponse
    {
        $orderFile->loadMissing('order');
        $user = $request->user();

        $allowed = in_array($user->role, ['admin', 'production', 'owner'], true)
            || ($user->role === 'customer' && $orderFile->order?->user_id === $user->id);

        abort_unless($allowed, 403);

        return $this->inlineResponse(
            $orderFile->file_path,
            $orderFile->file_name,
            'order-files'
        );
    }

    public function paymentProof(Request $request, Payment $payment): StreamedResponse
    {
        $payment->loadMissing('order');
        $user = $request->user();

        $allowed = in_array($user->role, ['admin', 'owner'], true)
            || ($user->role === 'customer' && $payment->order?->user_id === $user->id);

        abort_unless($allowed, 403);

        return $this->inlineResponse(
            $payment->payment_proof,
            basename((string) $payment->payment_proof),
            'payment-proofs'
        );
    }

    private function inlineResponse(
        ?string $storedPath,
        ?string $downloadName,
        string $expectedDirectory
    ): StreamedResponse {
        $path = StoragePath::normalize($storedPath);
        $prefix = trim($expectedDirectory, '/').'/';

        abort_if(
            $path === null
            || StoragePath::isUrl($path)
            || !str_starts_with($path, $prefix),
            404
        );

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response(
            $path,
            basename($downloadName ?: $path),
            [
                'Cache-Control' => 'private, no-store, max-age=0',
                'Pragma' => 'no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }
}

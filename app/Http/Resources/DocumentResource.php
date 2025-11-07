<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'amount' => [
                'value' => (float) $this->amount,
                'formatted' => $this->formatted_amount,
            ],
            'status' => $this->status ? [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'badge_color' => $this->status->badgeColor(),
                'icon' => $this->status->icon(),
            ] : null,
            'file' => $this->when($this->hasFile(), [
                'path' => $this->file_path,
                'name' => $this->file_name,
                'size' => $this->file_size,
            ]),
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),
            'history' => $this->whenLoaded('history', function () {
                return DocumentHistoryResource::collection($this->history);
            }),
            'permissions' => [
                'can_edit' => $this->canBeEdited() && $this->user_id === $request->user()?->id,
                'can_delete' => $this->isDraft() && $this->user_id === $request->user()?->id,
                'can_submit' => $this->isDraft() && $this->user_id === $request->user()?->id,
                'is_final' => $this->isFinal(),
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

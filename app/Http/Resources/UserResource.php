<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'initials' => $this->initials(),
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            
            // Relaciones opcionales
            'driver_profile' => $this->whenLoaded('driverProfile', function() {
                return new DriverProfileResource($this->driverProfile);
            }),
            
            // Permisos (si usa Spatie)
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->pluck('name');
            }),
            
            'permissions' => $this->when(
                $request->user()?->can('view-permissions'),
                $this->getAllPermissions()->pluck('name')
            ),
        ];
    }
}
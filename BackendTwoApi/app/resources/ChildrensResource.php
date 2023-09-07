<?php

namespace App\Resources;

use App\Models\Parents;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildrensResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    // : array
    {
        // return parent::toArray($request);
        return [
            'id'=> $this->id,
            'parent_id' => $this->parent_id,
            'name'=> $this->name,
            'route' => $this->route,
            // 'children' => ChildrensResource::collection($this->Agendas_secretariats),,
        ];
    }
}

<?php

namespace App\Resources;

use App\Constants\Is_children;
use App\Resources\ChildrensResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentsResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'route' => $this->route,
            'icon' => $this->icon,
            'is_children' => Is_children::Is_childrenChar()[$this->is_children] ,
            'children' => ChildrensResource::collection($this->Children),
        ];
    }
}

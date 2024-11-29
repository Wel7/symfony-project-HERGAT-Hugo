<?php
namespace App\Enum;

enum ProductStatus: string {
   case DISPONIBLE="Disponible";
   case RUPTURE="En rupture";
   case PRECOMMANDE="En précommande";

   public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabels(): array
    {
        return array_map(fn($case) => ucwords(str_replace('_', ' ', $case->value)), self::cases());
    }
}


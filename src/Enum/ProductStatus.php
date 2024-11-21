<?php
namespace App\Enum;

enum ProductStatus: string {
   case DISPONIBLE="Disponible";
   case RUPTURE="En rupture";
   case PRECOMMANDE="En précommande";
}
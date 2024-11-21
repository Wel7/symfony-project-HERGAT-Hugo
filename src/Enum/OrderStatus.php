<?php
namespace App\Enum;

use Symfony\Component\TypeInfo\Type\EnumType;
enum OrderStatus: string {
   case PREPARATION="En préparation";
   case EXPEDIEE="Expédiée";
   case LIVRE="Livrée";
   case ANNULE="Annulé";
}



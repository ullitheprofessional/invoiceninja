<?php namespace App\Ninja\Intents;

use Auth;
use Exception;
use App\Models\Product;


class ListProductsIntent extends ProductIntent
{
    public function process()
    {
        $account = Auth::user()->account;
        $products = Product::scope()
            ->orderBy('product_key')
            ->limit(10)
            ->get()
            ->transform(function($item, $key) use ($account) {
                $card = $item->present()->skypeBot($account);
                if ($this->entity(ENTITY_INVOICE)) {
                    $card->addButton('imBack', trans('texts.add_to_invoice'), trans('texts.add_to_invoice_command', ['product' => $item->product_key]));
                }
                return $card;
            });

        return $this->createResponse(SKYPE_CARD_CAROUSEL, $products);
    }
}
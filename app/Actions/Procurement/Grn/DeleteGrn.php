<?php

namespace App\Actions\Procurement\Grn;

use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\GoodsReceivedNoteItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteGrn
{
    public function execute(GoodsReceivedNote $grn): void
    {
        try {
            DB::transaction(function () use ($grn): void {
                $message = 'Only draft GRNs can be deleted';
                $check = $grn->getAttribute('status') !== 'draft';
                throw_if($check, new Exception($message));

                $id = $grn->id;
                GoodsReceivedNoteItem::whereGoodsReceivedNoteId($id)->delete();
                $grn->delete();

                success();
                redirect(GoodsReceivedNoteResource::getUrl());
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }
}

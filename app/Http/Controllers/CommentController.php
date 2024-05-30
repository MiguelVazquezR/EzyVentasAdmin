<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Admin;
use App\Models\SupportReport;
use App\Notifications\AdminBasicNotification;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Determine the model class from the request
        $modelClass = $request->commentable_type;
        $modelId = $request->commentable_id;

        // Ensure the model class exists
        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid commentable type'], 400);
        }

        // Find the related model
        $model = $modelClass::findOrFail($modelId);

        // Create the comment
        $comment = new Comment([
            'content' => $request->content,
            'admin_id' => auth()->id(),
        ]);

        $model->comments()->save($comment);

        // Notify the responsible person if not the commenter
        if ($model instanceof SupportReport) {
            $this->notifyStoreResponsible($model);
        }

        // Notify mentioned admins
        $this->notifyMentions($request->mentions, $model);

        return response()->json(['item' => $comment->fresh('admin:id,name,profile_photo_path')]);
    }

    protected function notifyStoreResponsible($supportReport)
    {
        if ($supportReport->store->seller_id && $supportReport->store->seller_id != auth()->id()) {
            $auth = auth()->user();
            $title = "Nuevo comentario en reporte de la tienda {$supportReport->store->name}";
            $description = "\"{$auth->name}\" ha hecho un comentario en el reporte #{$supportReport->id} perteneciente a la tienda llamada \"{$supportReport->store->name}\" de la cual tu eres responsable.";
            $url = route('support-reports.show', $supportReport->id);
            $supportReport->store->seller->notify(new AdminBasicNotification($title, $description, $url));
        }
    }

    protected function notifyMentions($mentions, $model)
    {
        if (empty($mentions)) {
            return;
        }

        $auth = auth()->user();
        foreach ($mentions as $mention) {
            $admin = Admin::find($mention['id']);
            if ($admin) {
                $title = "Te mencionaron en un comentario";
                $description = "\"{$auth->name}\" te ha mencionado en un comentario del reporte #{$model->id} perteneciente a la tienda llamada \"{$model->store->name}\".";
                $url = route('support-reports.show', $model->id);
                $admin->notify(new AdminBasicNotification($title, $description, $url));
            }
        }
    }
}

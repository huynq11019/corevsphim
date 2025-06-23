<?php

namespace Ophim\Core\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Ophim\Core\Models\Comment;

/**
 * Class MovieCrudController
 * @package Ophim\Core\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CommentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {
        destroy as traitDestroy;
    }

    use \Ophim\Core\Traits\Operations\BulkDeleteOperation {
        bulkDelete as traitBulkDelete;
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\Ophim\Core\Models\Comment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/comment');
        CRUD::setEntityNameStrings('comment', 'comments');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'user.name', 'label' => 'Người dùng', 'type' => 'text']);
        CRUD::addColumn(['name' => 'movie.name', 'label' => 'Phim', 'type' => 'text']);
        CRUD::addColumn(['name' => 'content', 'label' => 'Nội dung', 'type' => 'text']);
        // CRUD::addColumn(['name' => 'like', 'label' => 'Lượt thích', 'type' => 'number']);
        // CRUD::addColumn(['name' => 'dislike', 'label' => 'Lượt không thích', 'type' => 'number']);
        // CRUD::addColumn(['name' => 'report', 'label' => 'Lượt báo cáo', 'type' => 'number']);
    }
}

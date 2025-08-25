<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ShortsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Episode;

/**
 * Class EpisodeShortsController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EpisodeShortsController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Episode::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/episode-shorts');
        CRUD::setEntityNameStrings('short video', 'shorts videos');

        // Only show shorts episodes
        $this->crud->addClause('where', 'is_short', true);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Add columns for list view
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Tiêu đề',
            'type' => 'text',
            'limit' => 50
        ]);

        CRUD::addColumn([
            'name' => 'movie.name',
            'label' => 'Phim',
            'type' => 'text',
            'limit' => 30
        ]);

        CRUD::addColumn([
            'name' => 'hashtags',
            'label' => 'Hashtags',
            'type' => 'array',
            'limit' => 100
        ]);

        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Trạng thái',
            'type' => 'select_from_array',
            'options' => [
                'active' => 'Hoạt động',
                'inactive' => 'Không hoạt động',
                'pending' => 'Chờ duyệt'
            ]
        ]);

        CRUD::addColumn([
            'name' => 'view',
            'label' => 'Lượt xem',
            'type' => 'number'
        ]);

        CRUD::addColumn([
            'name' => 'likes',
            'label' => 'Likes',
            'type' => 'number'
        ]);

        CRUD::addColumn([
            'name' => 'dislikes',
            'label' => 'Dislikes',
            'type' => 'number'
        ]);

        CRUD::addColumn([
            'name' => 'duration_seconds',
            'label' => 'Thời lượng (giây)',
            'type' => 'number'
        ]);

        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Ngày tạo',
            'type' => 'datetime'
        ]);

        // Add filters
        $this->crud->addFilter([
            'type'  => 'dropdown',
            'name'  => 'status',
            'label' => 'Trạng thái'
        ], [
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
            'pending' => 'Chờ duyệt'
        ], function($value) {
            $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->addFilter([
            'type'  => 'simple',
            'name'  => 'hashtags',
            'label' => 'Hashtag'
        ], false, function($value) {
            $this->crud->addClause('where', 'hashtags', 'LIKE', "%{$value}%");
        });

        // Set default order
        $this->crud->orderBy('created_at', 'DESC');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ShortsRequest::class);

        // Basic info fields
        CRUD::addField([
            'name' => 'name',
            'label' => 'Tiêu đề Short',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Nhập tiêu đề cho video short...',
                'required' => true
            ]
        ]);

        CRUD::addField([
            'name' => 'slug',
            'label' => 'Slug',
            'type' => 'text',
            'hint' => 'Để trống để tự động tạo từ tiêu đề',
            'attributes' => [
                'placeholder' => 'auto-generated-slug'
            ]
        ]);

        // Movie selection
        CRUD::addField([
            'label' => 'Phim',
            'type' => 'select2',
            'name' => 'movie_id',
            'entity' => 'movie',
            'attribute' => 'name',
            'model' => "App\Models\Movie",
            'attributes' => [
                'required' => true
            ]
        ]);

        // Video link
        CRUD::addField([
            'name' => 'link',
            'label' => 'Link Video',
            'type' => 'textarea',
            'attributes' => [
                'placeholder' => 'Nhập URL video hoặc JSON data...',
                'rows' => 4,
                'required' => true
            ],
            'hint' => 'Có thể là URL trực tiếp hoặc JSON format cho multiple sources'
        ]);

        // Shorts specific fields
        CRUD::addField([
            'name' => 'hashtags',
            'label' => 'Hashtags',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Nhập hashtags cách nhau bởi dấu phẩy: #funny, #viral, #trending'
            ],
            'hint' => 'Phân cách bằng dấu phẩy. VD: #funny, #viral, #trending'
        ]);

        CRUD::addField([
            'name' => 'duration_seconds',
            'label' => 'Thời lượng (giây)',
            'type' => 'number',
            'attributes' => [
                'min' => 1,
                'max' => 300,
                'step' => 1
            ],
            'default' => 30,
            'hint' => 'Thời lượng video từ 1-300 giây'
        ]);

        CRUD::addField([
            'name' => 'status',
            'label' => 'Trạng thái',
            'type' => 'select_from_array',
            'options' => [
                'active' => 'Hoạt động',
                'inactive' => 'Không hoạt động',
                'pending' => 'Chờ duyệt'
            ],
            'default' => 'pending'
        ]);

        // Content description
        CRUD::addField([
            'name' => 'content',
            'label' => 'Mô tả',
            'type' => 'textarea',
            'attributes' => [
                'placeholder' => 'Mô tả ngắn gọn về nội dung video short...',
                'rows' => 3
            ]
        ]);

        // Statistics (for editing)
        CRUD::addField([
            'name' => 'view',
            'label' => 'Lượt xem',
            'type' => 'number',
            'default' => 0,
            'attributes' => [
                'min' => 0
            ]
        ]);

        CRUD::addField([
            'name' => 'likes',
            'label' => 'Likes',
            'type' => 'number',
            'default' => 0,
            'attributes' => [
                'min' => 0
            ]
        ]);

        CRUD::addField([
            'name' => 'dislikes',
            'label' => 'Dislikes',
            'type' => 'number',
            'default' => 0,
            'attributes' => [
                'min' => 0
            ]
        ]);

        CRUD::addField([
            'name' => 'shares',
            'label' => 'Shares',
            'type' => 'number',
            'default' => 0,
            'attributes' => [
                'min' => 0
            ]
        ]);

        // Hidden field to mark as short
        CRUD::addField([
            'name' => 'is_short',
            'type' => 'hidden',
            'value' => true
        ]);

        // Server field (required by Episode model)
        CRUD::addField([
            'name' => 'server',
            'type' => 'hidden',
            'value' => 'shorts'
        ]);

        // Type field (required by Episode model)
        CRUD::addField([
            'name' => 'type',
            'type' => 'hidden',
            'value' => 'embed'
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Show operation setup
     */
    protected function setupShowOperation()
    {
        $this->setupListOperation();

        // Add video preview
        CRUD::addColumn([
            'name' => 'video_preview',
            'label' => 'Video Preview',
            'type' => 'custom_html',
            'value' => function($entry) {
                $videoUrl = $entry->getVideoUrl();
                if ($videoUrl) {
                    return '<video width="300" height="200" controls>
                                <source src="' . $videoUrl . '" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>';
                }
                return 'No video available';
            }
        ]);

        // Add interactions summary
        CRUD::addColumn([
            'name' => 'interactions_summary',
            'label' => 'Tương tác',
            'type' => 'custom_html',
            'value' => function($entry) {
                $interactions = $entry->interactions()->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')->pluck('count', 'type')->toArray();

                $html = '<div class="badge-group">';
                $html .= '<span class="badge badge-info">Views: ' . ($entry->view ?? 0) . '</span> ';
                $html .= '<span class="badge badge-success">Likes: ' . ($entry->likes ?? 0) . '</span> ';
                $html .= '<span class="badge badge-warning">Dislikes: ' . ($entry->dislikes ?? 0) . '</span> ';
                $html .= '<span class="badge badge-primary">Shares: ' . ($entry->shares ?? 0) . '</span>';
                $html .= '</div>';

                return $html;
            }
        ]);
    }
}

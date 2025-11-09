<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Models\Course;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\View\PanelsRenderHook;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\EmbeddedTable;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\Pages\EditCourse;
use Filament\Schemas\Components\Grid;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    public function getTabs(): array
    {
        return [
            'past' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('start_at', '<', now()->subDay()->toDateString())),
            'today' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('start_at', '=', now()->toDateString())),
            'future' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('start_at', '>', now()->endOfDay()->toDateString())),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                Grid::make(3)->schema(
                    $this->getTableRecords()->sortBy('start_at')->map(function (Course $course) use ($schema) {
                        // Create a fresh schema instance for each course
                        $infolist = CourseResource::infolist($schema)
                            ->record($course)
                            ->getFlatComponents(withAbsoluteKeys: true);

                        return Section::make($course->title)
                            ->key((string) $course->id)
                            ->model($course)
                            ->schema($infolist)
                            ->footerActions([
                                Action::make('view')
                                    ->label('View course')
                                    ->url(fn () => EditCourse::getUrl(['record' => $course]))
                                    ->button(),
                            ]);
                    })->toArray()
                ),
            RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
        ]);
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

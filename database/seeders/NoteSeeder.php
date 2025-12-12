<?php

namespace Database\Seeders;

use App\Models\{User, Note, NoteFolder, NoteTag};
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        foreach ($users as $user) {
            $this->createNotesForUser($user);
        }

        $this->command->info('Notes seeded successfully!');
    }

    protected function createNotesForUser(User $user): void
    {
        // Create folders
        $folders = [
            ['name' => 'Personal', 'icon' => '🏠'],
            ['name' => 'Trabajo', 'icon' => '💼'],
            ['name' => 'Ideas', 'icon' => '💡'],
        ];

        $createdFolders = [];
        foreach ($folders as $folder) {
            $createdFolders[] = $user->noteFolders()->create($folder);
        }

        // Create tags
        $tags = [
            ['name' => 'Importante', 'color' => '#FBE4E4'],
            ['name' => 'Pendiente', 'color' => '#FBF3DB'],
            ['name' => 'Completado', 'color' => '#DDEDEA'],
            ['name' => 'Referencia', 'color' => '#DDEBF1'],
        ];

        $createdTags = [];
        foreach ($tags as $tag) {
            $createdTags[] = $user->noteTags()->create($tag);
        }

        // Create sample notes
        $notes = [
            [
                'title' => 'Bienvenido a Notas',
                'icon' => '👋',
                'content' => $this->getSampleContent('welcome'),
                'is_pinned' => true,
                'pinned_at' => now(),
            ],
            [
                'title' => 'Lista de tareas',
                'icon' => '✅',
                'folder_id' => $createdFolders[0]->id,
                'content' => $this->getSampleContent('tasks'),
            ],
            [
                'title' => 'Ideas para proyectos',
                'icon' => '💡',
                'folder_id' => $createdFolders[2]->id,
                'content' => $this->getSampleContent('ideas'),
            ],
            [
                'title' => 'Notas de reunión',
                'icon' => '📋',
                'folder_id' => $createdFolders[1]->id,
                'content' => $this->getSampleContent('meeting'),
            ],
        ];

        foreach ($notes as $index => $noteData) {
            $note = $user->notes()->create($noteData);
            
            // Add random tags
            if ($index > 0) {
                $note->tags()->attach($createdTags[array_rand($createdTags)]->id);
            }
        }
    }

    protected function getSampleContent(string $type): array
    {
        $contents = [
            'welcome' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => '¡Bienvenido!']]],
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Este es tu espacio para capturar ideas, tomar notas y organizar tus pensamientos.']]],
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Prueba escribir "/" para ver los comandos disponibles.']]],
                ],
            ],
            'tasks' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Tareas pendientes']]],
                    ['type' => 'taskList', 'content' => [
                        ['type' => 'taskItem', 'attrs' => ['checked' => true], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Crear cuenta']]]]],
                        ['type' => 'taskItem', 'attrs' => ['checked' => false], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Explorar la app']]]]],
                        ['type' => 'taskItem', 'attrs' => ['checked' => false], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Crear primer hábito']]]]],
                    ]],
                ],
            ],
            'ideas' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Ideas']]],
                    ['type' => 'bulletList', 'content' => [
                        ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Aprender algo nuevo cada día']]]]],
                        ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Meditar 10 minutos']]]]],
                        ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Leer 20 páginas']]]]],
                    ]],
                ],
            ],
            'meeting' => [
                'type' => 'doc',
                'content' => [
                    ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Notas de reunión']]],
                    ['type' => 'paragraph', 'content' => [['type' => 'text', 'marks' => [['type' => 'bold']], 'text' => 'Fecha: '], ['type' => 'text', 'text' => 'Hoy']]],
                    ['type' => 'heading', 'attrs' => ['level' => 3], 'content' => [['type' => 'text', 'text' => 'Puntos discutidos']]],
                    ['type' => 'orderedList', 'content' => [
                        ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Revisión de objetivos']]]]],
                        ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Próximos pasos']]]]],
                    ]],
                ],
            ],
        ];

        return $contents[$type] ?? ['type' => 'doc', 'content' => []];
    }
}

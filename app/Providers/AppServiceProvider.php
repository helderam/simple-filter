<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

#incluido 
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        // Preenche menu
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {

            $id = Auth::id();
            // Obtem menus permitidos e grupos
            $menus = DB::select(" 
                select g.id, g.name grupo, u.name, p.name programa, gu.user_id, gp.program_id, p.route
                from sac5.bi_groups g
                join sac5.bi_group_users gu on gu.group_id = g.id
                join sac5.bi_group_programs gp on gp.group_id = g.id
                join sac5.bi_users u on u.id = gu.user_id
                join sac5.bi_programs p on p.id = gp.program_id
                where gu.active = 'S' and gp.active = 'S'
                and gu.user_id = $id
                order by g.name desc, p.name 
                ");
            $route = substr($_SERVER['PATH_INFO'],1,100);
            $encontrei = false;
            $grupos = [];
            foreach ($menus as $menu) {
                $grupos[$menu->programa] = $menu->grupo;
                $routes[$menu->programa] = $menu->route;
                if ($menu->route == $route) $encontrei = true;
            }
            #var_dump($encontrei); var_dump($route);
            if (!$encontrei) {
                #return redirect()->to("/home");
                #->route('home')
                #->with('error', 'Sem permissão!');
                echo "SEM PERMISSÂO";
                exit;
            }

            $anterior = '';
            foreach ($grupos as $programa => $grupo) {
                if ($grupo != $anterior) {
                    $anterior = $grupo;
                    $event->menu->add($grupo);
                }
                $event->menu->add(['text' => $programa, 'url' => $routes[$programa]]);
            }

            $event->menu->add('CONFIGURAÇÔES');
            $event->menu->add(['text' => 'profile', 'url' => 'admin/settings', 'icon' => 'fas fa-fw fa-user']);

            /*
            #$event->menu->add($menusPermitidos[0]);
            $event->menu->add('MAIN NAVIGATION');
            $event->menu->add([
                'text' => 'Blog',
                'url' => 'admin/blog',
            ]);*/
        });
        /*
        [
            'text'    => 'GRUPO',
            'icon'    => 'fas fa-fw fa-share',
            'submenu' => [
                [
                    'text'        => 'pages',
                    
                    'icon'        => 'far fa-fw fa-file',
                    'label'       => 4,
                    'label_color' => 'success',
                ],
            ],
        ],*/
    }
}

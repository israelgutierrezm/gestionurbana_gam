import { animate, keyframes, style, transition, trigger } from '@angular/animations';
import { AfterViewInit, Component, EventEmitter, HostListener, OnInit, Output } from '@angular/core';
import { Router } from '@angular/router';

interface SideNavToggle {
  screenWidth: number;
  collapsed: boolean
}

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.scss'],
})
export class SidebarComponent implements OnInit {
  @Output() onToggleSideNav: EventEmitter<SideNavToggle> = new EventEmitter()

  collapsed: boolean = false;
  screenWidth: number = 0;
  navData: Array<any>;

  @HostListener('window:resize', ['$event'])
  onResize(event: any) {
    this.screenWidth = window.innerWidth;
    if (this.screenWidth <= 768) {
      this.collapsed = false;
      this.onToggleSideNav.emit({ collapsed: this.collapsed, screenWidth: this.screenWidth })
    }
  }

  constructor(
    private router: Router,
  ) {
    this.screenWidth = window.innerWidth;
    this.navData = [{
      routeLink: 'home',
      icon: 'fa fa-home',
      label: 'Inicio'
    },
    {
      routeLink: 'personal',
      icon: 'fa fa-users',
      label: 'Usuarios'
    },
    /*
    {
      routeLink: 'frentes/consulta',
      icon: 'fa-solid fa-route',
      label: 'Frentes'
    },
    {
      routeLink: 'actividades',
      icon: 'fa-solid fa-people-carry-box',
      label: 'Actividades'
    },
    {
      routeLink: 'inventario',
      icon: 'fa-solid fa-screwdriver-wrench',
      label: 'Herramientas'
    },
    {
      routeLink: 'reportes',
      icon: 'fa-solid fa-chart-simple',
      label: 'Reportes',
    },*/
    {
      routeLink: '',
      icon: 'fa fa-sign-out',
      label: 'Cerrar sesión',
      isLogout: '1'
    },
    ]
  }


  ngOnInit(): void {
    // this.muestraItems
  }

  toggleCollapse(): void {
    this.collapsed = !this.collapsed
    this.onToggleSideNav.emit({ collapsed: this.collapsed, screenWidth: this.screenWidth })
  }

  closeSidenav(): void {
    this.collapsed = false
    this.onToggleSideNav.emit({ collapsed: this.collapsed, screenWidth: this.screenWidth })
  }

  cerrarSesion() {
    localStorage.clear();
    this.router.navigate(['login']);
  }
}

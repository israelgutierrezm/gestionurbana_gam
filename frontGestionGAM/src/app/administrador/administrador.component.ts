import { Component } from '@angular/core';

interface SideNavToggle {
  screenWidth: number;
  collapsed: boolean
}

@Component({
  selector: 'app-administrador',
  templateUrl: './administrador.component.html',
  styleUrls: ['./administrador.component.scss']
})
export class AdministradorComponent {
  isSideNavCollapsed: boolean = false;
  screenWidth: number = 0;
  
    onToggleSideNav(data: SideNavToggle):void {
      this.screenWidth = data.screenWidth;
      this.isSideNavCollapsed = data.collapsed;
      
    }
}

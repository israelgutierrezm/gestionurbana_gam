import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { FormGroup } from '@angular/forms';
import { CatalogosService } from 'src/app/catalogosServices/catalogos.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {
  @Input() frenteForm!: FormGroup; 
  @Output() submitForm = new EventEmitter<void>(); // Evento que se emite al guardar
  direccionesTerritoriales: Array<any> = [];
  coloniasGAM: Array<any> = [];
  tiposEspaciosPublicos: Array<any> = [];


  constructor(
    private _catalogoService: CatalogosService,
  ) {
  }

  get frenteFormControls() { return this.frenteForm.controls; }

  ngOnInit() {
    this.consultaDireccionesTerritoriales();
    this.consultaEspaciosPublicos();
    this.consultaColonias();
  }

  consultaDireccionesTerritoriales() {
    this._catalogoService.getDireccionesTerritoriales().subscribe({
      next: (response: any) => {
        console.log(response); 
        if(response && response['estatus']){
          this.direccionesTerritoriales = response['catalogo'];
        }
      }
    });
  }

  consultaEspaciosPublicos() {
    this._catalogoService.getTiposEspaciosPublicosDT().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.tiposEspaciosPublicos = response['catalogo'];
        }
      }
    });
  }

  consultaColonias() {
    this._catalogoService.getColoniasGAM().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.coloniasGAM = response['catalogo'];
        }
      }
    });
  }

}

import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { FrentesService } from '../services/frentes.service';
import { ToastService } from 'src/app/extras/toast/toast.service';

declare var alertify: any;

@Component({
  selector: 'app-consulta',
  templateUrl: './consulta.component.html',
  styleUrls: ['./consulta.component.scss']
})
export class ConsultaComponent implements OnInit {
  frenteForm: FormGroup;
  arrayFrentes: any[] = []; 
  bandera_edicion: boolean = false;
  frenteId: string | null = null;
  nombreFrente: string = ''; 
  qrCode: string = ''; 
  frentesFiltrados: any[] = []; 
  nombreBusqueda: string = ''; 
  

  constructor(
    private formBuilder: FormBuilder,
    private modalService: NgbModal,
    private _toast: ToastService,
    private _frentesService: FrentesService

  ) {
    this.frenteForm = this.formBuilder.group({
      cat_direccion_territorial_id: ['', Validators.required],
      cat_colonia_id: ['', Validators.required],
      cat_tipo_espacio_frente_id: [[], Validators.required], 
      nombre: ['', Validators.required],
      area: ['', Validators.required],
      dias_jornada: ['', Validators.required],
      personal_necesario: ['', Validators.required],
      
    });
   
  }

  ngOnInit() {
    this.consultaFrentes();
  }


  consultaFrentes(){
    this._frentesService.consultaFrentes().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayFrentes = response['frentes'];
          this.frentesFiltrados = response['frentes'];
          
        }
      }
    });
  }

  filtrarFrentes() {
    console.log(this.nombreBusqueda);
    if (this.nombreBusqueda) {
      const nombreBusquedaLower = this.nombreBusqueda.toLowerCase();
      this.frentesFiltrados = this.arrayFrentes.filter(frente =>
        frente.nombre.toLowerCase().includes(nombreBusquedaLower)
      );
    } else {
      this.frentesFiltrados = this.arrayFrentes;
    }
  }

   descargarQR() {

  }
  
  openQRModal(contentModal: any, frente: any) {
    this.nombreFrente = frente.nombre;
    this.qrCode = "https://qrcode.tec-it.com/API/QRCode?data=https://estudy.com.mx/1";
    this.modalService.open(contentModal);
  }
  openModal(content: any) {
    this.bandera_edicion = false; 
    this.frenteId = null; 
    this.frenteForm.reset();
    this.modalService.open(content, { size: 'lg' });
  }

  closeModal() {
    this.bandera_edicion = false; 
    this.frenteId = null; 
    this.modalService.dismissAll();
  }

  creaArray(cadena: string | null | undefined): string[] {
    if (!cadena) {
      return [];
    }
    return cadena.split(',').map(valor => valor.trim());
  }
  

  openEditModal(content: any, frente: any) {
    var arrayFrentes: any[] = this.creaArray(frente.tipos_espacios_ids)
    this.bandera_edicion = true; 
    this.frenteId = frente.frente_id; 
    this.frenteForm.patchValue({
        cat_direccion_territorial_id: frente.cat_direccion_territorial_id,
        cat_colonia_id: frente.cat_colonia_id,
        nombre: frente.nombre,
        area: frente.area,
        dias_jornada: frente.dias_jornada,
        personal_necesario: frente.personal_necesario,
        cat_tipo_espacio_frente_id: arrayFrentes
    });

    this.modalService.open(content, { size: 'lg' });
}

  enviarInformacion() {
    if (this.frenteForm.valid) {
      this._frentesService.guardaFrente(this.frenteForm, this.frenteId).subscribe({
        next: (response: any) => {
          if(response && response['estatus']){
            this._toast.show(response['msg'], { classname: 'bg-success' });
            this.frenteForm.reset();
            this.modalService.dismissAll();
            this.consultaFrentes();
          }else{
            this._toast.show(response['msg'], { classname: 'bg-danger' });
          }
        }
      });

    } else 
      this.frenteForm.markAllAsTouched();
    
  }

  alertEliminaFrente(indiceFrente: number, frenteId: string) {
    alertify.confirm('', '¿Deseas eliminar este frente?',
      () => this.eliminaFrente(indiceFrente, frenteId),
      () => this.cancelado()
      ).set('labels', { ok: 'Sí', cancel: 'No' });
  }

  eliminaFrente(indiceFrente: number, frenteId:string) {
    this._frentesService.eliminaFrente(frenteId).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this.arrayFrentes.splice(indiceFrente, 1);
          this._toast.show('Eliminado correctamente', { classname: 'bg-success' });
        }
      }
    });
  }

  cancelado() {
    this._toast.show('Cancelado', { classname: 'bg-danger' });
  }
}



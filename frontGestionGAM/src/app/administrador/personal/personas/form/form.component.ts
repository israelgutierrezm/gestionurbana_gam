import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { CatalogosService } from 'src/app/catalogosServices/catalogos.service';
import { ToastService } from 'src/app/toast/toast.service';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})

export class FormComponent implements OnInit {
  personaForm: FormGroup;
  tiposSangre: Array<any> = [];

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private _toast: ToastService,
    private _catalogoService: CatalogosService
  ) {
    this.personaForm = this.formBuilder.group({
      nombre: ['', Validators.required],
      apellidoPaterno: ['', Validators.required],
      apellidoMaterno: ['', Validators.required],
      curp: ['', [Validators.required, Validators.pattern(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)]],
      sexo: ['', Validators.required],
      numeroTelefono: ['', Validators.required],
      enfermedades: [''],
      alergias: [''],
      medicamentos: [''],
      tipoSangre: ['', Validators.required],
      nombreContacto: ['', Validators.required],
      parentescoContacto: ['', Validators.required],
      numeroContacto: ['', Validators.required],
      rol: ['', Validators.required]
    });
  }

  get personaFormControls() { return this.personaForm.controls; }

  ngOnInit() {
    this.consultaCatTipoSangre();
  }

  enviarInformacion() {
    if (this.personaForm.invalid) {
      Object.keys(this.personaForm.controls).forEach(controlKey => {
        this.personaForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this._toast.show('Información guardada correctamente', { classname: 'bg-success text-light' });
    this.router.navigate(['/admin/personal/consulta']);
  }

  getSexo() {
    if (this.personaFormControls['curp'].status == 'VALID') {
      const curp = this.personaFormControls['curp'].value
      const sexo = curp[10];
      this.personaForm.get('sexo')?.setValue(sexo);
    }
  }

  consultaCatTipoSangre() {
    this._catalogoService.getCatalogoTipoSangre().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.tiposSangre = response['catalogo'];
        }
      }
    });
  }

}


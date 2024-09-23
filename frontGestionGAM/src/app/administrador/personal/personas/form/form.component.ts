import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { CatalogosService } from 'src/app/catalogosServices/catalogos.service';
import { ToastService } from 'src/app/toast/toast.service';
import { PersonalService } from '../../services/personal.service';
import { UsuarioForm } from './form-personas.model';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})


export class FormComponent implements OnInit {
  personaForm: FormGroup;
  tiposSangre: Array<any> = [];
  arregloRoles: Array<any> = [];
  arregloGeneros: Array<any> = [];
  usuarioId: string | null;

  usuarioList: UsuarioForm | null = null;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private route: ActivatedRoute,
    private _toast: ToastService,
    private _catalogoService: CatalogosService,
    private _personalService: PersonalService
  ) {
    this.usuarioId = this.route.snapshot.paramMap.get('usuarioId');
    this.personaForm = this.formBuilder.group({
      rol: ['', Validators.required],
      nombre: ['', Validators.required],
      apellidoPaterno: ['', Validators.required],
      apellidoMaterno: ['', Validators.required],
      curp: ['', [Validators.required, Validators.pattern(/^([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)$/)]],
      sexo: ['', Validators.required],
      fechaNacimiento: ['', Validators.required],
      numeroTelefono: ['', Validators.required],
      numeroCelular: ['', Validators.required],
      email: ['', Validators.required],
      nombreContacto: ['', Validators.required],
      apellidoContacto: ['', Validators.required],
      parentescoContacto: ['', Validators.required],
      numeroContacto: ['', Validators.required],
      enfermedades: [''],
      alergias: [''],
      medicamentos: [''],
      tipoSangre: ['', Validators.required]
    });
  }

  get personaFormControls() { return this.personaForm.controls; }

  ngOnInit() {
    if(this.usuarioId){
      this.consultaUsuarioForm();
    }
    this.consultaCatTipoSangre();
    this.consultaRoles();
    this.consultaGeneros();
  }

  getSexo() {
    if (this.personaFormControls['curp'].status == 'VALID') {
      const curp = this.personaFormControls['curp'].value
      const sexo = curp[10];
      if(sexo == 'H'){
        this.personaForm.get('sexo')?.setValue(1);
      }

      if(sexo == 'M'){
        this.personaForm.get('sexo')?.setValue(2);
      }
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

  consultaRoles() {
    this._catalogoService.getCatalogoRoles().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.arregloRoles = response['catalogo'];
        }
      }
    });
  }

  consultaGeneros() {
    this._catalogoService.getCatalogoGeneros().subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.arregloGeneros = response['catalogo'];
        }
      }
    });
  }

  consultaUsuarioForm(){
    this._personalService.consultaEspPersona(this.usuarioId).subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this.usuarioList = response['usuario'];
          console.log(this.usuarioList);
          this.fillForm();
        }
      }
    });
  }

  fillForm(){
    this.personaForm.get('rol')?.setValue(this.usuarioList?.cat_rol_id);
    this.personaForm.get('nombre')?.setValue(this.usuarioList?.nombre);
    this.personaForm.get('apellidoPaterno')?.setValue(this.usuarioList?.ap_pat);
    this.personaForm.get('apellidoMaterno')?.setValue(this.usuarioList?.ap_mat);
    this.personaForm.get('curp')?.setValue(this.usuarioList?.curp);
    this.personaForm.get('sexo')?.setValue(this.usuarioList?.cat_genero_id);
    this.personaForm.get('fechaNacimiento')?.setValue(this.usuarioList?.fecha_nacimiento);
    this.personaForm.get('numeroTelefono')?.setValue(this.usuarioList?.telefono);
    this.personaForm.get('numeroCelular')?.setValue(this.usuarioList?.celular);
    this.personaForm.get('email')?.setValue(this.usuarioList?.email);
    this.personaForm.get('nombreContacto')?.setValue(this.usuarioList?.nombre_contacto);
    this.personaForm.get('apellidoContacto')?.setValue(this.usuarioList?.apellido_contacto);
    this.personaForm.get('parentescoContacto')?.setValue(this.usuarioList?.parentesco);
    this.personaForm.get('numeroContacto')?.setValue(this.usuarioList?.telefono_contacto);
    this.personaForm.get('enfermedades')?.setValue(this.usuarioList?.condiciones_preexistentes ?? '');
    this.personaForm.get('alergias')?.setValue(this.usuarioList?.alergias ?? '');
    this.personaForm.get('medicamentos')?.setValue(this.usuarioList?.medicamentos ?? '');
    this.personaForm.get('tipoSangre')?.setValue(this.usuarioList?.tipo_sangre);
  }

  enviarInformacion() {
    if (this.personaForm.invalid) {
      Object.keys(this.personaForm.controls).forEach(controlKey => {
        this.personaForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this._personalService.guardaPersona(this.personaForm, this.usuarioId).subscribe({
      next: (response: any) => {
        if(response && response['estatus']){
          this._toast.show(response['msg'], { classname: 'bg-success text-light' });
          this.router.navigate(['/admin/personal/consulta']);
        }else{
          this._toast.show(response['msg'], { classname: 'bg-danger text-light' });
        }
      }
    });
  }

}


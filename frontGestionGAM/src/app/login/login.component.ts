import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ToastService } from '../extras/toast/toast.service';
import { LoginService } from './services/login.service';
import { PlataformaService } from '../services/plataforma.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  loginForm: FormGroup;
  showFooter: boolean = true;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private _toast: ToastService,
    private _loginService: LoginService,
    private _plataformaService: PlataformaService
  ) {
    this.loginForm = this.formBuilder.group({
      user: ['', Validators.required],
      pass: ['', Validators.required],
    });
  }

  get loginFormControls() { return this.loginForm.controls; }

  log() {
    if (this.loginForm.invalid) {
      Object.keys(this.loginForm.controls).forEach(controlKey => {
        this.loginForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this.validaUserPass();
  }

  validaUserPass() {
    localStorage.clear();
    this._loginService._validaUserPass(this.loginForm).subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          this._toast.show('Inicio de sesión exitoso', { classname: 'bg-success' });
          localStorage.setItem('jwt', JSON.stringify(response['jwt']));
          this.redirectRol();
        } else {
          this._toast.show(response['msg'], { classname: 'bg-danger' });
        }
      }
    });
  }

  redirectRol() {
    this._plataformaService.getJWTData().subscribe({
      next: (response: any) => {
        if (response && response['estatus']) {
          const rolId = response['usuario'].rol_id
          switch (rolId) {
            case '1':
              this.router.navigate(['admin']);
              break;
              case '4':
              this.router.navigate(['supervisor']);
              break;
            default:
              break;
          }
        } else {
          this._toast.show(response['msg'], { classname: 'bg-danger' });
        }
      }
    });
  }
}

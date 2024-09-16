import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  loginForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router
  ) {
    this.loginForm = this.formBuilder.group({
      correo: ['', Validators.required],
      pass: ['', Validators.required],
    });
  }

  get loginFormControls() { return this.loginForm.controls; }

  ngOnInit() {

  }

  enviarInformacion(){
    if (this.loginForm.invalid) {
      Object.keys(this.loginForm.controls).forEach(controlKey => {
        this.loginForm.controls[controlKey].markAsTouched();
      });
      return;
    }
    this.router.navigate(['admin']);
  }
}

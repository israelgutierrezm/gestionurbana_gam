import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {
  @Input() frenteForm!: FormGroup; 
  @Output() submitForm = new EventEmitter<void>(); // Evento que se emite al guardar

  constructor() {}

  ngOnInit(): void {}

  onSubmit() {
    if (this.frenteForm.valid) {
      this.submitForm.emit(); // Emitir el evento al guardar
    }
  }
}

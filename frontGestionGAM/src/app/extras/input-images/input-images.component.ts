import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';
import { ToastService } from '../toast/toast.service';
import { FormBuilder} from '@angular/forms';

@Component({
  selector: 'app-input-images',
  templateUrl: './input-images.component.html',
  styleUrls: ['./input-images.component.scss']
})
export class InputImagesComponent implements OnChanges {
  @Input() title: string = '';
  @Output() imagenFileChange = new EventEmitter<File | null>();

  imagePreview: string | ArrayBuffer | null = null;
  imagen!: File | null;

  constructor(
    private toast: ToastService,
    private fb: FormBuilder
  ) { }

  ngOnChanges(changes: SimpleChanges): void {

  }


  onFileSelected(event: any): void {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];

      const fileType = file.type;
      const validImageTypes = ['image/jpg', 'image/jpeg', 'image/png'];
      
      // Valida que sea un archivo de imagen
      if (validImageTypes.includes(fileType)) {
        this.imagen = file;
        this.cargaVistaPreviaImg(file);
        this.imagenFileChange.emit(this.imagen);
      } else {
        this.toast.show('Sólo debes ingresar imágenes', { classname: 'bg-danger' });
        this.imagen = null;
        this.imagePreview = null;
        this.imagenFileChange.emit(null);
      }
    }
  }

  cargaVistaPreviaImg(file: File){
    const reader = new FileReader();
    reader.onload = (e: any) => {
      this.imagePreview = e.target.result;
    };
    reader.readAsDataURL(file)
  }
}

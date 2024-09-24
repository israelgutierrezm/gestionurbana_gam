import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FrentesComponent } from './frentes.component';

describe('FrentesComponent', () => {
  let component: FrentesComponent;
  let fixture: ComponentFixture<FrentesComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [FrentesComponent]
    });
    fixture = TestBed.createComponent(FrentesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

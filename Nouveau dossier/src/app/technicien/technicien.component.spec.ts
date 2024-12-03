import { ComponentFixture, TestBed } from '@angular/core/testing';
import { TechnicienComponent } from './technicien.component'; // Importez TechnicienComponent

describe('TechnicienComponent', () => {  // Changez ici aussi en TechnicienComponent
  let component: TechnicienComponent;  // Assurez-vous que le type est TechnicienComponent
  let fixture: ComponentFixture<TechnicienComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TechnicienComponent]  // Ajoutez TechnicienComponent ici
    })
    .compileComponents();

    fixture = TestBed.createComponent(TechnicienComponent);  // Créez le composant TechnicienComponent ici
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PlanningTechnicienComponent } from '../../technicien/planning/planning-technicien.component';

describe('PlanningTechnicienComponent', () => {
  let component: PlanningTechnicienComponent;
  let fixture: ComponentFixture<PlanningTechnicienComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PlanningTechnicienComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PlanningTechnicienComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

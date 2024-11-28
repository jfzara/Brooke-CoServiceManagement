import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { InterventionService, STATUS_TYPES } from '../../services/intervention.service';

@Component({
  selector: 'app-intervention-edit',
  templateUrl: './intervention-edit.component.html',
  styleUrls: ['./intervention-edit.component.css'],
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule]
})
export class InterventionEditComponent implements OnInit {
  editForm: FormGroup;
  interventionId: number | null = null;
  readonly STATUS_TYPES = STATUS_TYPES;

  constructor(
    private fb: FormBuilder,
    private interventionService: InterventionService,
    private route: ActivatedRoute,
    private router: Router
  ) {
    this.editForm = this.fb.group({
      TechnicienID: ['', Validators.required],
      ClientID: ['', Validators.required],
      TypeIntervention: ['', Validators.required],
      Description: ['', Validators.required],
      DebutIntervention: ['', Validators.required],
      FinIntervention: ['', Validators.required],
      StatutIntervention: ['', Validators.required],
      Commentaires: ['']
    });
  }

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.interventionId = +id;
      this.loadIntervention(this.interventionId);
    }
  }

  loadIntervention(id: number) {
    this.interventionService.getInterventionById(id).subscribe({
      next: (intervention) => {
        this.editForm.patchValue(intervention);
      },
      error: (error) => {
        console.error('Erreur lors du chargement de l\'intervention:', error);
      }
    });
  }

  onSubmit() {
    if (this.editForm.valid && this.interventionId) {
      const interventionData = this.editForm.value;
      this.interventionService.updateIntervention(this.interventionId, interventionData)
        .subscribe({
          next: () => {
            this.router.navigate(['/technicien']);
          },
          error: (error) => {
            console.error('Erreur lors de la mise à jour:', error);
          }
        });
    }
  }
}
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { InterventionService } from '../../services/intervention.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';

interface Intervention {
  Description: string;
  Date: string;
  Heure: string;
  Statut: string;
  TechnicienID: number;
  Commentaires: string;
}

interface Technicien {
  TechnicienID: number;
  Nom: string;
  Prenom: string;
}

@Component({
  selector: 'app-intervention-edit',
  templateUrl: './intervention-edit.component.html',
  styleUrls: ['./intervention-edit.component.css'],
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule
  ]
})
export class InterventionEditComponent implements OnInit {
  editForm: FormGroup;
  interventionId: number = 0;
  techniciens: Technicien[] = [];
  statuts: string[] = ['Planifiee', 'Assignee', 'En cours', 'Terminee', 'Annulee'];
  isLoading: boolean = false;

  constructor(
    private fb: FormBuilder,
    private route: ActivatedRoute,
    private router: Router,
    private interventionService: InterventionService,
    private snackBar: MatSnackBar
  ) {
    this.editForm = this.fb.group({
      description: ['', [Validators.required, Validators.minLength(10)]],
      date: ['', Validators.required],
      heure: ['', Validators.required],
      statut: ['', Validators.required],
      technicienId: ['', Validators.required],
      commentaires: ['']
    });
  }

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.interventionId = +id;
      this.loadInterventionDetails();
      this.loadTechniciens();
    } else {
      this.showError('ID d\'intervention non trouvé');
      this.router.navigate(['/planning']);
    }
  }

  loadInterventionDetails(): void {
    this.isLoading = true;
    this.interventionService.getInterventionById(this.interventionId)
      .subscribe({
        next: (data: Intervention) => {
          this.editForm.patchValue({
            description: data.Description,
            date: data.Date,
            heure: data.Heure,
            statut: data.Statut,
            technicienId: data.TechnicienID,
            commentaires: data.Commentaires
          });
          this.isLoading = false;
        },
        error: (error: Error) => {
          this.showError('Erreur lors du chargement des détails');
          console.error('Erreur lors du chargement des détails:', error);
          this.isLoading = false;
        }
      });
  }

  loadTechniciens(): void {
    this.interventionService.getTechniciens()
      .subscribe({
        next: (data: Technicien[]) => {
          this.techniciens = data;
        },
        error: (error: Error) => {
          this.showError('Erreur lors du chargement des techniciens');
          console.error('Erreur lors du chargement des techniciens:', error);
        }
      });
  }

  onSubmit(): void {
    if (this.editForm.valid) {
      this.isLoading = true;
      const formData = {
        ...this.editForm.value,
        InterventionID: this.interventionId
      };

      this.interventionService.updateIntervention(this.interventionId, formData)
        .subscribe({
          next: () => {
            this.showSuccess('Intervention mise à jour avec succès');
            this.router.navigate(['/planning']);
          },
          error: (error: Error) => {
            this.showError('Erreur lors de la mise à jour');
            console.error('Erreur lors de la mise à jour:', error);
            this.isLoading = false;
          }
        });
    } else {
      this.showError('Veuillez remplir tous les champs obligatoires correctement');
    }
  }

  private showSuccess(message: string): void {
    this.snackBar.open(message, 'Fermer', {
      duration: 3000,
      panelClass: ['success-snackbar']
    });
  }

  private showError(message: string): void {
    this.snackBar.open(message, 'Fermer', {
      duration: 5000,
      panelClass: ['error-snackbar']
    });
  }

  // Getters pour la validation des formulaires
  get description() { return this.editForm.get('description'); }
  get date() { return this.editForm.get('date'); }
  get heure() { return this.editForm.get('heure'); }
  get statut() { return this.editForm.get('statut'); }
  get technicienId() { return this.editForm.get('technicienId'); }
}
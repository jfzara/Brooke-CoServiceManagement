// src/app/prepose/dashboard/prepose-dashboard.component.ts
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@app/core/services/auth.service';
import { UserProfile } from '@app/models/user.model';

@Component({
  selector: 'app-prepose-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    FormsModule
  ],
  templateUrl: './prepose-dashboard.component.html',
  styleUrls: ['./prepose-dashboard.component.css']
})
export class PreposeDashboardComponent implements OnInit {
  userProfile: UserProfile;

  constructor(
    private authService: AuthService
  ) {
    this.userProfile = this.authService.userProfile;
  }

  ngOnInit(): void {
    // Initialisation du dashboard
  }

  logout(): void {
    this.authService.logout();
  }
}
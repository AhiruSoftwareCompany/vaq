import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CookieService } from 'ngx-cookie-service';

import { DataService } from '../../services/data.service';
import { ContextService } from '../../services/context.service';
import { Quote } from '../../models/quote';

@Component({
  selector: 'app-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.css']
})
export class MainComponent implements OnInit {
    public currentQuote: Quote = null;
    public legalOrigins: String[];

    public constructor(
        private dataService: DataService,
        private context: ContextService,
        private router: Router,
        private cookie: CookieService) {
    }

    public ngOnInit(): void {
        if (this.context.getUser() === null) {
            this.router.navigate(['/login']);
        } else {
            this.legalOrigins = this.context.getUser().origins;
            this.getNewQuote();
        }
    }

    public getNewQuote(): void {
        let selectedOrigins = [];
        let originInputs = document.getElementsByClassName("origin");
        [].forEach.call(originInputs, function(input) {
            if (input.checked)
                selectedOrigins.push(input.value);
        })
        console.log(selectedOrigins);

        this.dataService.getRandomQuote(selectedOrigins)
        .then(quote => {
            this.currentQuote = quote;
        });
    }

    public refreshRating(vote: number): void {
        this.currentQuote.vote = vote;
        let ratingAN = Number(this.currentQuote.rating);
        this.dataService.refreshRating(this.currentQuote)
        .then(diff => {
            this.currentQuote.rating = ratingAN + diff;
        });
    }

    public logout(): void {
        this.cookie.delete('PHPSESSID');
        this.router.navigate(['/login']);
    }
}

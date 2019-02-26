import { Component, OnInit } from '@angular/core';

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
    public origins: String[];

    public constructor(
        private dataService: DataService,
        private context: ContextService) {
    }

    public ngOnInit(): void {
        this.getNewQuote();
        this.origins = this.context.getUser().origins;
    }

    public getNewQuote(): void {
        this.dataService.getRandomQuote()
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
}

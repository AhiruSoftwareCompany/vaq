import { Component, OnInit } from '@angular/core';
import { QuoteService } from '../../services/quote.service';
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
        private quoteService: QuoteService) {
    }

    public ngOnInit(): void {
        this.getOrigins();
        this.getNewQuote();
    }

    public getNewQuote(): void {
        this.quoteService.getRandomQuote()
        .then(quote => {
            this.currentQuote = quote;
        });
    }

    public refreshRating(vote: number): void {
        this.currentQuote.vote = vote;
        let ratingAN = Number(this.currentQuote.rating);
        this.quoteService.refreshRating(this.currentQuote)
        .then(diff => {
            this.currentQuote.rating = ratingAN + diff;
        });
    }

    public getOrigins(): void {
        this.quoteService.getOrigins()
        .then(origins => {
            this.origins = origins;
        });
    }
}

import {fireEvent, render, screen} from '@testing-library/react';
import {CreateFlashCardPage} from '../pages/CreateFlashCardPage';

describe('CreateFlashCardPage component', () => {

        beforeEach(() => {
            global.fetch = jest.fn(() =>
                Promise.resolve({
                    json: () => Promise.resolve({status: 201}),
                })
            );
        });

        afterEach(() => {
            jest.clearAllMocks();
        });


        test('renders without crashing', () => {
            render(<CreateFlashCardPage/>);
        });

        test('displays form fields correctly', () => {
            render(<CreateFlashCardPage/>);
            const questionInput = screen.getByTestId('question-input');
            const answerInput = screen.getByTestId('answer-input');
            const tagInput = screen.getByTestId('tag-input');
            expect(questionInput).toBeInTheDocument();
            expect(answerInput).toBeInTheDocument();
            expect(tagInput).toBeInTheDocument();
        });

        test('should show a form with 3 inputs and a button', () => {
                render(<CreateFlashCardPage/>);
                const form = screen.getByTestId('create-card-form');
                const questionInput = screen.getByTestId('question-input');
                const answerInput = screen.getByTestId('answer-input');
                const tagInput = screen.getByTestId('tag-input');
                const submitButton = screen.getByRole('button');
                expect(form).toContainElement(questionInput);
                expect(form).toContainElement(answerInput);
                expect(form).toContainElement(tagInput);
                expect(form).toContainElement(submitButton);
            }
        );

        test('should submit form correctly', async () => {
            render(<CreateFlashCardPage/>);

            fireEvent.change(screen.getByTestId('question-input'), {
                target: {value: 'What is React?'},
            });
            fireEvent.change(screen.getByTestId('answer-input'), {
                target: {value: 'A JavaScript library for building user interfaces'},
            });
            fireEvent.change(screen.getByTestId('tag-input'), {
                target: {value: 'JavaScript'},
            });

            fireEvent.click(screen.getByText('Create Card'));

            expect(global.fetch).toHaveBeenCalledTimes(1);
            expect(global.fetch).toHaveBeenCalledWith(expect.stringContaining('/cards'), expect.objectContaining({
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    question: 'What is React?',
                    answer: 'A JavaScript library for building user interfaces',
                    tag: 'JavaScript',
                }),
            }));

        });


        test('should handle API call failure correctly', async () => {
            global.fetch.mockImplementationOnce(() =>
                Promise.resolve({
                    status: 400,
                    json: () => Promise.resolve({error: 'Failed to create the card.'}),
                })
            );

            render(<CreateFlashCardPage/>);

            fireEvent.change(screen.getByTestId('question-input'), {
                target: {value: 'What is React?'},
            });
            fireEvent.change(screen.getByTestId('answer-input'), {
                target: {value: 'A JavaScript library for building user interfaces'},
            });
            fireEvent.change(screen.getByTestId('tag-input'), {
                target: {value: 'JavaScript'},
            });

            fireEvent.click(screen.getByText('Create Card'));

            expect(global.fetch).toHaveBeenCalledTimes(1);

            const errorMessage = await screen.findByText(/Failed to create the card./i);
            expect(errorMessage).toBeInTheDocument();
        });


    }
);
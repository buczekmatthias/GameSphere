export interface Constants {
    form: {
        description: {
            min_length: number;
        };
        files: {
            media: {
                accept_type: string;
                max_files: number;
            };
            thumbnail: {
                accept_type: string;
            };
        };
    };
}

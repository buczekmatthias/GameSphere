export interface Constants {
    form: {
        description: {
            min_length: number;
        };
        files: {
            media: {
                accept_type: string;
                max_files: number;
                max_size: number;
                max_size_display: string;
            };
            thumbnail: {
                accept_type: string;
            };
        };
    };
}

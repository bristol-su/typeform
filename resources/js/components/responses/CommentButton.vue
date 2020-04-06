<template>
    <div>
        <b-button :can-add-comments="canAddComments"
                  :can-delete-comments="canDeleteComments"
                  :can-update-comments="canUpdateComments"
                  :response-id="responseId"
                  v-b-modal="'comment-button-' + this.responseId"
                  variant="outline-info">
            <span>
                Comments 
                <b-badge variant="secondary">{{numberOfComments}} <span class="sr-only">{{numberOfComments}} comments</span></b-badge>
            </span>
        </b-button>

        <b-modal :id="'comment-button-' + this.responseId">
            <comments
                    :can-add-comments="canAddComments"
                    :can-delete-comments="canDeleteComments"
                    :can-update-comments="canUpdateComments"
                    :response-id="responseId"
                    @comment-added="addComment"
                    @set-comment-count="additionalComments = ($event - commentCount)"></comments>
        </b-modal>
    </div>
</template>

<script>
    import Comments from './Comments';

    export default {
        name: "CommentButton",
        components: {Comments},
        props: {
            canAddComments: {
                required: false,
                default: false
            },
            canDeleteComments: {
                required: false,
                default: false
            },
            canUpdateComments: {
                required: false,
                default: false
            },
            responseId: {
                required: true,
                type: String
            },
            commentCount: {
                required: false,
                type: Number,
                default: 0
            }
        },

        data() {
            return {
                additionalComments: 0
            }
        },

        methods: {
            addComment(comment) {
                this.additionalComments += 1;
            },
            deleteComment() {
                this.additonalComments -= 1;
            }
        },

        computed: {
            numberOfComments() {
                return this.commentCount + this.additionalComments;
            }
        }
    }
</script>

<style scoped>

</style>
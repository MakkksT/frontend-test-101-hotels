<template>
    <div class="page">
        <div class="container">

            <h1>Комментарии ({{ comments.length }})</h1>

            <div class="sorting">
                <label>Сортировка:</label>

                <select v-model="sortBy">
                    <option value="id">По ID</option>
                    <option value="date">По дате</option>
                </select>

                <select v-model="sortDirection">
                    <option value="asc">По возрастанию</option>
                    <option value="desc">По убыванию</option>
                </select>
            </div>

            <div
                v-for="comment in paginatedComments"
                :key="comment.id"
                class="comment"
            >
                <div class="comment-header">

                    <div class="avatar">
                        {{ getAvatarLetter(comment.name) }}
                    </div>

                    <div class="comment-info">
                        <h3>{{ comment.name }}</h3>

                        <div class="rating">
                            <span>Оценка: {{ comment.rating }}/10</span>

                            <strong v-if="comment.rating === 10">
                                Великолепно!
                            </strong>
                        </div>
                    </div>

                </div>

                <p>{{ comment.text }}</p>

                <small>{{ comment.date }}</small>

                <div class="comment-actions">
                    <button @click="deleteComment(comment.id)">
                        Удалить
                    </button>
                </div>

                <hr>
            </div>

            <div
                v-if="totalPages > 1"
                class="pagination"
            >
                <button
                    v-for="page in totalPages"
                    :key="page"
                    :class="{ active: currentPage === page }"
                    @click="currentPage = page"
                >
                    {{ page }}
                </button>
            </div>

            <h2>Добавить комментарий</h2>

            <form @submit.prevent="submitComment">

                <div>
                    <label>Имя</label>

                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Введите имя"
                    >
                </div>

                <div>
                    <label>Комментарий</label>

                    <textarea
                        v-model="form.text"
                        placeholder="Введите комментарий"
                    ></textarea>
                </div>

                <div>
                    <label>Дата</label>

                    <DatePicker
                        v-model="form.date"
                        value-type="format"
                        format="YYYY-MM-DD"
                        placeholder="Выберите дату"
                    />
                </div>

                <div>
                    <label>Оценка</label>

                    <select v-model.number="form.rating">
                        <option
                            v-for="rating in 10"
                            :key="rating"
                            :value="rating"
                        >
                            {{ rating }}
                        </option>
                    </select>

                    <div
                        v-if="form.rating === 10"
                        class="rating-message"
                    >
                        Великолепно!
                    </div>
                </div>

                <button type="submit">
                    Добавить комментарий
                </button>

            </form>

        </div>
    </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'

export default {

    components: {
        DatePicker
    },

    data() {
        return {
            form: {
                name: '',
                text: '',
                date: '',
                rating: 5
            },

            sortBy: 'id',
            sortDirection: 'desc',

            currentPage: 1,
            perPage: 3
        }
    },

    computed: {
        comments() {
            return this.$store.state.comments
        },

        sortedComments() {
            return [...this.comments].sort((a, b) => {
                let result

                if (this.sortBy === 'id') {
                    result = a.id - b.id
                } else {
                    result = new Date(a.date) - new Date(b.date)
                }

                return this.sortDirection === 'asc'
                    ? result
                    : -result
            })
        },

        totalPages() {
            return Math.ceil(
                this.sortedComments.length / this.perPage
            )
        },

        paginatedComments() {
            const start = (this.currentPage - 1) * this.perPage
            const end = start + this.perPage

            return this.sortedComments.slice(start, end)
        }
    },

    watch: {
        totalPages(newTotalPages) {
            if (this.currentPage > newTotalPages) {
                this.currentPage = Math.max(newTotalPages, 1)
            }
        }
    },

    mounted() {
        this.$store.dispatch('fetchComments')
    },

    methods: {
        async submitComment() {
            const newComment = await this.$store.dispatch(
                'addComment',
                this.form
            )

            const newCommentIndex = this.sortedComments.findIndex(
                comment => comment.id === newComment.id
            )

            if (newCommentIndex !== -1) {
                this.currentPage = Math.floor(
                    newCommentIndex / this.perPage
                ) + 1
            }

            this.form = {
                name: '',
                text: '',
                date: '',
                rating: 5
            }
        },

        async deleteComment(id) {
            await this.$store.dispatch(
                'deleteComment',
                id
            )
        },

        getAvatarLetter(name) {
            if (!name) {
                return '?'
            }

            return name.trim().charAt(0).toUpperCase()
        }
    }
}
</script>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    color: #222;
}

.page {
    min-height: 100vh;
    padding: 30px 15px;
}

.container {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    padding: 30px;
    background: #fff;
    border-radius: 10px;
}

button,
input,
textarea,
select {
    font: inherit;
}

button,
select,
input,
textarea {
    border: 1px solid #ccc;
    border-radius: 6px;
}

button {
    padding: 8px 14px;
    background: #fff;
    cursor: pointer;
}

button:hover {
    background: #f0f0f0;
}

input,
textarea,
select {
    margin-bottom: 10px;
    padding: 10px;
    width: 100%;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

form {
    max-width: 600px;
}

form > div {
    margin-bottom: 15px;
}

form label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
}

.sorting {
    margin-bottom: 25px;
}

.sorting select {
    width: auto;
    min-width: 180px;
    margin-left: 10px;
}

.comment {
    padding: 10px 0;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 45px;
    height: 45px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eee;
    font-size: 20px;
    font-weight: bold;
}

.comment-info h3 {
    margin: 0 0 5px;
}

.rating {
    display: flex;
    gap: 10px;
    font-size: 14px;
}

.rating-message {
    margin-top: 5px;
    font-weight: bold;
}

.comment-actions {
    margin-top: 10px;
}

.pagination {
    display: flex;
    gap: 5px;
    margin: 20px 0;
}

.pagination button.active {
    font-weight: bold;
}

h1 {
    margin-bottom: 20px;
}

h2 {
    margin-top: 35px;
}

hr {
    margin: 20px 0;
    border: 0;
    border-top: 1px solid #ddd;
}

@media (max-width: 600px) {
    .page {
        padding: 10px;
    }

    .container {
        padding: 20px 15px;
        border-radius: 6px;
    }

    h1 {
        font-size: 26px;
    }

    h2 {
        font-size: 22px;
    }

    .sorting select {
        display: block;
        width: 100%;
        margin: 10px 0 0;
    }

    button {
        min-height: 40px;
    }

    .rating {
        flex-direction: column;
        gap: 3px;
    }
}
</style>
